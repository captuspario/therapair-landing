import { MessageSquare, Users, Calendar } from "lucide-react";
import { Card } from "./ui/card";

export function HowItWorksSection() {
  const steps = [
    {
      icon: MessageSquare,
      title: "Tell us about yourself",
      description: "Answer a few questions in a friendly, judgment-free chat about your needs and preferences.",
    },
    {
      icon: Users,
      title: "We match you with the right therapist",
      description: "Our AI considers your identity, goals, and therapeutic style preferences to find your perfect match.",
    },
    {
      icon: Calendar,
      title: "Connect, book, and begin",
      description: "Review your matches, book a consultation, and start your journey toward better mental health.",
    },
  ];

  return (
    <section className="py-16 px-4 sm:px-6 lg:px-8 bg-white/50">
      <div className="max-w-6xl mx-auto">
        <div className="text-center mb-12">
          <h2 className="mb-4 text-3xl sm:text-4xl">How does it work?</h2>
          <p className="text-lg opacity-80 max-w-2xl mx-auto">
            Finding the right therapist shouldn't be complicated. Our process is designed to be simple, supportive, and respectful of your unique needs.
          </p>
        </div>
        
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {steps.map((step, index) => (
            <Card key={index} className="p-8 text-center border border-[var(--therapair-rosewood)]/20 bg-white/80 backdrop-blur-sm rounded-2xl hover:shadow-lg transition-all duration-300">
              <div className="w-16 h-16 mx-auto mb-6 rounded-full bg-[var(--therapair-calm-clay)]/20 flex items-center justify-center">
                <step.icon className="w-8 h-8 text-[var(--therapair-rosewood)]" />
              </div>
              <div className="text-2xl mb-2">{index + 1}</div>
              <h3 className="mb-4 text-xl">{step.title}</h3>
              <p className="opacity-80 leading-relaxed">{step.description}</p>
            </Card>
          ))}
        </div>
      </div>
    </section>
  );
}