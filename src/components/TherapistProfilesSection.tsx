import { Card } from "./ui/card";
import { Button } from "./ui/button";
import { Badge } from "./ui/badge";
import { ImageWithFallback } from "./figma/ImageWithFallback";

export function TherapistProfilesSection() {
  const therapists = [
    {
      name: "Dr. Alex Chen",
      image: "https://images.unsplash.com/photo-1733685318562-c726472bc1db?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHx0aGVyYXBpc3QlMjBwb3J0cmFpdCUyMHByb2Zlc3Npb25hbHxlbnwxfHx8fDE3NTk5MDM2OTl8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
      style: "Warm, direct, and solution-focused",
      specialties: ["LGBTQ+ affirming", "Anxiety", "Career transitions"],
    },
    {
      name: "Maya Rodriguez, LCSW",
      image: "https://images.unsplash.com/photo-1730272768147-2b1442f8a965?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxkaXZlcnNlJTIwcGVvcGxlJTIwcG9ydHJhaXQlMjBwcm9mZXNzaW9uYWx8ZW58MXx8fHwxNzU5OTAzNzAxfDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
      style: "Gentle, culturally responsive approach",
      specialties: ["Trauma-informed", "Bilingual", "Family therapy"],
    },
    {
      name: "Jordan Kim, PhD",
      image: "https://images.unsplash.com/photo-1613618958001-ee9ad8f01f9c?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtZW50YWwlMjBoZWFsdGglMjBzdXBwb3J0JTIwY29tbXVuaXR5fGVufDF8fHx8MTc1OTkwMzY5N3ww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
      style: "Collaborative and strengths-based",
      specialties: ["Neurodiversity", "ADHD", "Executive functioning"],
    },
  ];

  return (
    <section className="py-16 px-4 sm:px-6 lg:px-8">
      <div className="max-w-6xl mx-auto">
        <div className="text-center mb-12">
          <h2 className="mb-4 text-3xl sm:text-4xl">Meet some of our therapists</h2>
          <p className="text-lg opacity-80 max-w-2xl mx-auto">
            Every therapist in our network is carefully vetted for cultural competence and inclusive practice.
          </p>
        </div>
        
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {therapists.map((therapist, index) => (
            <Card key={index} className="overflow-hidden border border-[var(--therapair-rosewood)]/20 bg-white/80 backdrop-blur-sm rounded-2xl hover:shadow-lg transition-all duration-300">
              <div className="aspect-square relative overflow-hidden">
                <ImageWithFallback
                  src={therapist.image}
                  alt={`Portrait of ${therapist.name}`}
                  className="w-full h-full object-cover"
                />
              </div>
              <div className="p-6">
                <h3 className="mb-2 text-xl">{therapist.name}</h3>
                <p className="mb-4 text-sm opacity-80 italic">{therapist.style}</p>
                <div className="mb-4">
                  <p className="text-sm mb-2">Good for:</p>
                  <div className="flex flex-wrap gap-2">
                    {therapist.specialties.map((specialty, specIndex) => (
                      <Badge 
                        key={specIndex} 
                        variant="secondary" 
                        className="bg-[var(--therapair-calm-clay)]/20 text-[var(--therapair-rosewood)] border-[var(--therapair-rosewood)]/30"
                      >
                        {specialty}
                      </Badge>
                    ))}
                  </div>
                </div>
                <Button 
                  className="w-full bg-[var(--therapair-rosewood)] hover:bg-[var(--therapair-terracotta)] text-white rounded-xl"
                  data-role="book-therapist"
                  data-therapist={therapist.name}
                >
                  Book Now
                </Button>
              </div>
            </Card>
          ))}
        </div>
        
        <div className="text-center mt-8">
          <p className="text-sm opacity-70">
            *Profiles are for demonstration purposes. Actual matching will be based on your specific needs and preferences.
          </p>
        </div>
      </div>
    </section>
  );
}